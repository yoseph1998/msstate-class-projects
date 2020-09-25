package finch.robot;

import finch.robot.commands.Gravitometer;
import finch.robot.commands.ObstacleEvader;
import finch.robot.commands.RelaySight;
import finch.robot.framework.Command;
import finch.robot.input.KeyBinder;

import java.awt.event.KeyEvent;

/**
 * The Driver, All the human operated control occurs here.
 */
public class RobotDriver {

    private static final String START_RELAYSIGHT = "START_RELAYSIGHT";
    private static final String START_OBSTACLEEVADER = "START_OBSTACLEEVADER";
    private static final String START_GRAVITOMETER = "START_GRAVITOMETER";

    private Command program1, program2, program3;

    public void init() {
        KeyBinder.getInstance().addBinding(START_RELAYSIGHT, KeyEvent.VK_I);
        KeyBinder.getInstance().addBinding(START_OBSTACLEEVADER, KeyEvent.VK_O);
        KeyBinder.getInstance().addBinding(START_GRAVITOMETER, KeyEvent.VK_A);

        program1 = new RelaySight("Relay Sight (Program 1)");
        program2 = new ObstacleEvader("Obstacle Evader (Program 2)");
        program3 = new Gravitometer("Gravitometer (Program 3)");

        Robot.getInstance().getWindow().addText("Robot Ready!");
        Robot.getInstance().getWindow().addText("Press I to run " + program1.getName());
        Robot.getInstance().getWindow().addText("Press O to run " + program2.getName());
        Robot.getInstance().getWindow().addText("Press A to run " + program3.getName());
    }

    public void update() {
        if (KeyBinder.getInstance().isBindingJustPressed(START_RELAYSIGHT))
            program1.start();
        if (KeyBinder.getInstance().isBindingJustPressed(START_OBSTACLEEVADER))
            program2.start();
        if (KeyBinder.getInstance().isBindingJustPressed(START_GRAVITOMETER))
            program3.start();
    }

    public void end() {

    }
}
